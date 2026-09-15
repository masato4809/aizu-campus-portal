import * as React from 'react';
import { DocumentNode, useApolloClient } from '@apollo/client';
import { IMutationResult, parseErrors } from '@/script/Hooks/IMutationResult';
import { useAppPersonalSettingSpPasswordMutation } from '@/script/Graphql/codegen/graphql';
import { E_STATUS_CODE } from '@/script/Enum/Server/App/EStatusCode';

export interface IAppPersonalSettingSpPassword {
  password: string;
}

export interface IAppPersonalSettingSpPasswordResult extends IMutationResult {
  url: string;
}

export function useMutationAppPersonalSettingSpPassword(): [
  boolean,
  (
    props: IAppPersonalSettingSpPassword,
    refetchQueries: DocumentNode[],
  ) => Promise<IAppPersonalSettingSpPasswordResult>,
] {
  const [sending, setSending] = React.useState<boolean>(false);
  const [mutation] = useAppPersonalSettingSpPasswordMutation({});
  const client = useApolloClient();

  /**
   * 送信関数.
   */
  const mutationAppPersonalSettingSpPassword = async (
    props: IAppPersonalSettingSpPassword,
    refetchQueries: DocumentNode[],
  ): Promise<IAppPersonalSettingSpPasswordResult> => {
    setSending(true);

    return new Promise<IAppPersonalSettingSpPasswordResult>(resolve => {
      mutation({
        variables: {
          password: String(props.password),
        },
      }).then(async response => {
        const statusCode = Number(
          response.data?.AppPersonalSettingSpPassword?.statusCode,
        );

        if (statusCode === E_STATUS_CODE.OK && refetchQueries.length) {
          await client.refetchQueries({
            include: refetchQueries,
          });
        }

        // プロセス終了を通知.
        setSending(false);

        resolve({
          statusCode,
          statusMessage: String(
            response.data?.AppPersonalSettingSpPassword?.statusMessage,
          ),
          errors: parseErrors(
            response.data?.AppPersonalSettingSpPassword?.errors,
          ),
          url: String(response.data?.AppPersonalSettingSpPassword?.url),
        });
      });
    });
  };

  return [sending, mutationAppPersonalSettingSpPassword];
}
