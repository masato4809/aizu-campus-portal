import { ApolloError } from '@apollo/client';
import {
  AppTrnUserList,
  IAppTrnUser,
  parseAppTrnUserPayload,
} from '@/script/Models/App/Trn/TrnUserList';
import { useAppTrnUserListQuery } from '@/script/Graphql/codegen/graphql';

export function useFetchAppTrnUserList(): [
  boolean,
  AppTrnUserList,
  ApolloError | undefined,
] {
  /**
   * クエリ実施.
   */
  const query = useAppTrnUserListQuery({});

  // データのparse.
  const list = new AppTrnUserList(
    query.data?.AppTrnUserList.map(v =>
      parseAppTrnUserPayload(v as IAppTrnUser),
    ),
  );

  return [query.loading, list, query.error];
}
