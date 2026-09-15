import * as React from 'react';
import { useSampleRedisLazyQuery } from '@/script/Graphql/codegen/graphql';

export interface IAppRedis {
  sampleArgument: string;
}

export function useLazyFetchSampleRedis(): [
  boolean,
  (props: IAppRedis) => Promise<string>,
] {
  const [sending, setSending] = React.useState<boolean>(false);
  const [lazyQuery] = useSampleRedisLazyQuery({});

  const lazyFetch = async (props: IAppRedis): Promise<string> => {
    setSending(true);

    return new Promise<string>(resolve => {
      lazyQuery({
        variables: {
          sampleArgument: props.sampleArgument,
        },
        fetchPolicy: 'no-cache',
      }).then(async response => {
        setSending(false);

        resolve(response.data?.SampleRedis.storeValue ?? '');
      });
    });
  };

  return [sending, lazyFetch];
}
