import { ApolloError } from '@apollo/client';
import { useAppTrnProjectListQuery } from '@/script/Graphql/codegen/graphql';
import {
  AppTrnProjectList,
  IAppTrnProject,
  parseAppTrnProjectPayload,
} from '@/script/Models/App/Trn/TrnProjectList';

export function useFetchAppTrnProjectList(): [
  boolean,
  AppTrnProjectList,
  ApolloError | undefined,
] {
  /**
   * クエリ実施.
   */
  const query = useAppTrnProjectListQuery({});

  // データのparse.
  const list = new AppTrnProjectList(
    query.data?.AppTrnProjectList.map(v =>
      parseAppTrnProjectPayload(v as IAppTrnProject),
    ),
  );

  return [query.loading, list, query.error];
}
